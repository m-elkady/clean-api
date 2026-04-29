/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_API_URL?: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}

declare module 'primeicons/primeicons.css' {
  const content: { [className: string]: string }
  export default content
}
