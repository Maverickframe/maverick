# MaverickFrame WordPress Theme

## Install
```
npm install
```

Create `.env` file in the root of the project and add your local development settings. Example:

```
IS_VITE_DEVELOPMENT=true
VITE_THEME_PATH=/maverickframe/wp-content/themes/maverickframe
VITE_ASSETS_PORT=3000
VITE_LOCAL_SERVER=//localhost:8888/maverickframe
VITE_ENTRY_POINT = /src/js/bundle.js
VITE_STYLES = /src/scss/main.scss
VITE_STYLES_NEW = /src/scss/new.scss
VITE_STYLES_BLOCKS = /src/scss/blocks.scss
```

## Usage
Start server for development:
```
npm start
```

Create build for production:
```
npm run build
```

Lint JavaScript files:
```
npm run lint
```
