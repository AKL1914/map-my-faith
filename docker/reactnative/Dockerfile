FROM node:18

# Set working directory
WORKDIR /app

# Copy package.json and install dependencies (if they exist)
COPY package*.json ./
RUN if [ -f package.json ]; then npm install; fi

# Copy the rest of the app
COPY . .

# Expose Metro Bundler port
EXPOSE 19000

# Start Expo development server (assumes Expo will be used)
CMD ["npx", "expo", "start", "--tunnel"]