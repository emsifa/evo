import React from "react";
import clsx from "clsx";
import Layout from "@theme/Layout";
import Link from "@docusaurus/Link";
import useDocusaurusContext from "@docusaurus/useDocusaurusContext";
import styles from "./index.module.css";
import HomepageFeatures from "../components/HomepageFeatures";

function HomepageHeader() {
  const { siteConfig } = useDocusaurusContext();
  return (
    <header className={clsx(styles.heroBanner)}>
      <div className="mx-auto py-6 max-w-screen-lg ">
        <div className="flex items-center leading-10 flex-wrap">
          <div className="text-center md:text-left flex-1">
            <h1 className="text-3xl md:text-6xl font-bold">
              {siteConfig.title}
            </h1>
            <p className="text-xl md:text-3xl mb-10">{siteConfig.tagline}</p>
            <Link
              className="bg-gray-600 text-white text-lg px-3 py-2 rounded md:px-4 md:py-3 md:text-xl md:rounded-lg hover:no-underline hover:text-white hover:shadow-xl"
              href="/docs/getting-started/intro"
            >
              Get Started
            </Link>
          </div>
          <div className="w-full mt-12 md:w-[400px] md:mt-0">
            <img
              className="w-full rounded-xl overflow-hidden shadow-2xl"
              src="img/hero-image.svg"
              alt=""
            />
          </div>
        </div>
      </div>
    </header>
  );
}

export default function Home(): JSX.Element {
  const { siteConfig } = useDocusaurusContext();
  return (
    <Layout
      title={`Hello from ${siteConfig.title}`}
      description="Description will go into a meta tag in <head />"
    >
      <HomepageHeader />
    </Layout>
  );
}
