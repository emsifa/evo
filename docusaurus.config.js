const lightCodeTheme = require("prism-react-renderer/themes/oceanicNext");
const darkCodeTheme = require("prism-react-renderer/themes/oceanicNext");

// With JSDoc @type annotations, IDEs can provide config autocompletion
/** @type {import('@docusaurus/types').DocusaurusConfig} */
(
  module.exports = {
    title: "Laravel Evo",
    tagline: "Use Laravel future, today.",
    url: "https://emsifa.github.io/evo",
    baseUrl: "/evo/",
    onBrokenLinks: "throw",
    onBrokenMarkdownLinks: "warn",
    favicon: "img/favicon.ico",
    organizationName: "emsifa", // Usually your GitHub org/user name.
    projectName: "evo", // Usually your repo name.
    trailingSlash: true,

    presets: [
      [
        "@docusaurus/preset-classic",
        /** @type {import('@docusaurus/preset-classic').Options} */
        ({
          docs: {
            sidebarPath: require.resolve("./sidebars.js"),
            // Please change this to your repo.
            editUrl: "https://github.com/emsifa/evo/edit/main/website/",
          },
          // blog: {
          //   showReadingTime: true,
          //   // Please change this to your repo.
          //   editUrl: "https://github.com/emsifa/evo/edit/main/website/blog/",
          // },
          theme: {
            customCss: require.resolve("./src/css/custom.css"),
          },
        }),
      ],
    ],

    themeConfig:
      /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
      ({
        colorMode: {
          defaultMode: "dark",
        },
        navbar: {
          title: "",
          logo: {
            alt: "My Site Logo",
            src: "img/logo-black.svg",
            srcDark: "img/logo-white.svg",
          },
          items: [
            {
              type: "doc",
              docId: "getting-started/intro",
              position: "left",
              label: "Documentation",
            },
            {
              href: "https://github.com/emsifa/evo",
              label: "GitHub",
              position: "right",
            },
          ],
        },
        footer: {
          style: "dark",
          links: [
            {
              title: "Docs",
              items: [
                {
                  label: "Introduction",
                  to: "/docs/getting-started/intro",
                },
                {
                  label: "Getting Started",
                  to: "/docs/getting-started/installation",
                },
              ],
            },
            {
              title: "Community",
              items: [
                {
                  label: "Stack Overflow",
                  href: "https://stackoverflow.com/questions/tagged/laravel-evo",
                },
                // {
                //   label: "Discord",
                //   href: "https://discordapp.com/invite/laravel-evo",
                // },
                {
                  label: "Twitter",
                  href: "https://twitter.com/laravel-evo",
                },
              ],
            },
            {
              title: "More",
              items: [
                // {
                //   label: "Blog",
                //   to: "/blog",
                // },
                {
                  label: "GitHub",
                  href: "https://github.com/emsifa/evo",
                },
              ],
            },
          ],
          copyright: `Copyright © ${new Date().getFullYear()} Laravel Evo. Built with Docusaurus.`,
        },
        prism: {
          theme: lightCodeTheme,
          darkTheme: darkCodeTheme,
          additionalLanguages: ["php"],
        },
      }),
  }
);
