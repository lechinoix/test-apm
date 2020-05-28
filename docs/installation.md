# Installation

In development:
- webpack-dev-server listens on the port 3000
  - it serves your frontend app's bundle.js
  - it manages hot reloading


To build the application, launch:
```bash
docker-compose build
```



## Start the app

What you need to do to (re)start the project:

- start the frontend:
  ```bash
  cd frontend
  yarn install
  yarn start
  ```

  The project should now be running at [localhost:3000](http://localhost:3000).

- start backend and frontend:
  ```bash
  docker-compose up -d
  ```

The project should now be running at [https://localhost](https://localhost).
