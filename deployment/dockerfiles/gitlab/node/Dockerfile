FROM node:14.3-alpine

COPY ./ .

RUN npm rebuild node-sass --force
RUN yarn install
RUN yarn run prod
