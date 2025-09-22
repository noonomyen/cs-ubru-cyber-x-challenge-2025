FROM node:current-alpine AS builder

WORKDIR /build

RUN corepack enable

COPY package.json pnpm-lock.yaml ./

RUN pnpm install --frozen-lockfile

COPY tsconfig.json .
COPY src ./src
COPY public ./public

RUN pnpm build

FROM node:current-alpine AS runtime

WORKDIR /app

RUN corepack enable

COPY package.json pnpm-lock.yaml ./

RUN pnpm install --prod

COPY --from=builder /build/dist ./dist

CMD ["pnpm", "start"]
