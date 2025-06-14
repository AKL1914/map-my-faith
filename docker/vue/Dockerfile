FROM node:18-slim

WORKDIR /app

COPY package*.json ./

CMD ["npm", "install"]

RUN npm install
# Clean npm cache and force install all deps inside the container
#RUN npm cache clean --force && npm install

COPY . .

CMD ["npm", "run", "dev"]
