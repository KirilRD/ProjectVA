declare module "next/link" {
  import type { ComponentType, ReactNode } from "react";
  interface LinkProps {
    href: string;
    className?: string;
    children?: ReactNode;
    prefetch?: boolean;
  }
  const Link: ComponentType<LinkProps>;
  export default Link;
}
