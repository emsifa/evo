<?php

namespace Emsifa\Evo\Commands;

use Emsifa\Evo\Dto;
use Emsifa\Evo\Types\ArrayOf;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeDtoCommand extends Command
{
    public $signature = 'evo:make-dto {file} {properties?*}';

    public $description = 'Generate DTO file';

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    public function handle()
    {
        $file = $this->argument('file');
        $properties = $this->argument('properties');

        $path = $this->getOutputPath($file);
        if (file_exists($path)) {
            return $this->error("Dto '{$file}' already exists.");
        }

        $className = $this->getDtoClassName($file);
        $properties = $this->resolveProperties($properties);
        $content = $this->generateFileContent($className, $properties);

        $this->saveFile($path, $content);
        foreach ($properties as $prop) {
            if ($prop["filePath"]) {
                $content = $this->generateFileContent($prop["className"], []);
                $this->saveFile($prop["filePath"], $content);
            }
        }
    }

    protected function saveFile(string $path, string $content)
    {
        $this->makeDirectory($path);
        $this->files->put($path, $content);
        $filepath = str_replace(app_path(), "", $path);
        $this->info("Created DTO: {$filepath}");
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    protected function generateFileContent(string $fullClassName, array $properties): string
    {
        [$namespace, $className] = $this->splitNamespace($fullClassName);
        $parentClassName = Dto::class;
        $extends = $this->getClassName($parentClassName);

        $uses = [$parentClassName, ...$this->getUses($properties, $fullClassName)];

        $bodyLines = $this->getBodyLines($properties);

        $lines = [
            "<?php",
            "",
            "namespace {$namespace};",
            "",
            ...array_map(fn ($use) => "use {$use};", $uses),
            "",
            "class {$className} extends {$extends}",
            "{",
            ...array_map(fn ($line) => $line ? "    {$line}" : "", $bodyLines),
            "}",
            "",
        ];

        return implode("\n", $lines);
    }

    protected function getUses(array $properties, string $fullClassName): array
    {
        $uses = [];
        $arrayOfClass = ArrayOf::class;
        foreach ($properties as $prop) {
            if ($prop["className"] && ! $this->isSameNamespace($fullClassName, $prop["className"])) {
                $uses[] = $prop["className"];
            }
            if ($prop["isTypedArray"] && ! in_array($arrayOfClass, $uses)) {
                $uses[] = $arrayOfClass;
            }
        }

        return $uses;
    }

    protected function getBodyLines(array $properties): array
    {
        $bodyLines = [];
        foreach ($properties as $prop) {
            if ($prop["isTypedArray"]) {
                $typeName = $this->isBuiltInType($prop["type"])
                    ? "'{$prop['type']}'"
                    : $this->getClassName($prop["type"])."::class";

                $bodyLines = [
                    ...$bodyLines,
                    "",
                    "#[ArrayOf({$typeName})]",
                    "public array \${$prop['name']};",
                    "",
                ];
            } else {
                $nullableSign = $prop["isNullable"] ? "?" : "";
                $bodyLines[] = "public {$nullableSign}{$prop['type']} \${$prop['name']};";
            }
        }
        // @codeCoverageIgnoreStart
        if (count($bodyLines) && $bodyLines[count($bodyLines) - 1] === "") {
            array_pop($bodyLines);
        }
        // @codeCoverageIgnoreEnd

        return $bodyLines;
    }

    protected function isSameNamespace(string $a, string $b): bool
    {
        return $this->getClassNamespace($a) == $this->getClassNamespace($b);
    }

    protected function splitNamespace(string $fullClassName): array
    {
        return [$this->getClassNamespace($fullClassName), $this->getClassName($fullClassName)];
    }

    protected function getClassNamespace(string $fullClassName): string
    {
        $split = explode("\\", $fullClassName);
        array_pop($split);

        return count($split) ? implode("\\", $split) : "";
    }

    protected function getClassName(string $fullClassName): string
    {
        $split = explode("\\", $fullClassName);

        return array_pop($split);
    }

    protected function getOutputPath(string $file)
    {
        $path = "Dtos/{$file}.php";
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);

        return app_path($path);
    }

    protected function resolveProperties(array $properties): array
    {
        $results = [];
        foreach ($properties as $prop) {
            [$name, $type, $isNullable, $isTypedArray] = $this->parseProperty($prop);

            $isBuiltInType = $this->isBuiltInType($type);

            $results[] = [
                "name" => $name,
                "type" => $isBuiltInType ? $type : pathinfo($type, PATHINFO_FILENAME),
                "isNullable" => $isNullable,
                "isTypedArray" => $isTypedArray,
                "className" => ! $isBuiltInType ? $this->getTypeClassName($type) : null,
                "filePath" => ! $isBuiltInType && ! class_exists($type) ? $this->getOutputPath($type) : null,
            ];
        }

        return $results;
    }

    protected function isBuiltInType(string $type)
    {
        return in_array($type, ["int", "float", "bool", "string", "array", "mixed", "object"]);
    }

    protected function getTypeClassName(string $type)
    {
        if (class_exists($type)) {
            return $type;
        }

        return $this->getDtoClassName($type);
    }

    protected function getDtoClassName(string $type): string
    {
        return "App\\Dtos\\" . str_replace("/", "\\", $type);
    }

    protected function parseProperty(string $prop)
    {
        $splitProp = explode(":", $prop);
        if (count($splitProp) === 1) {
            return [$prop, 'mixed', false, false];
        }

        [$name, $type] = $splitProp;
        $isTypedArray = substr($type, -2, 2) === "[]";

        if ($isTypedArray) {
            $type = substr($type, 0, -2);
        }

        $isNullable = substr($type, 0, 1) == "?";
        if ($isNullable) {
            $type = substr($type, 1);
        }

        return [$name, $type, $isNullable, $isTypedArray];
    }
}
