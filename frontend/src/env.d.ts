typescriptdeclare namespace NodeJS {
    interface ProcessEnv {
      NEXT_PUBLIC_API_URL?: string;
    }
  }
  
  declare const process: {
    env: NodeJS.ProcessEnv;
  };