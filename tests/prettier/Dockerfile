FROM node:21.7.1-alpine

WORKDIR /prettier

COPY . .

RUN yarn install \
 && yarn cache clean

ENTRYPOINT [ "node", "/prettier/node_modules/.bin/prettier" ]

CMD [ \
    "/K-pi", \
    "--write", \
    "--cache", \
    "--cache-location=/K-pi/.prettier.cache", \
    "--config=/K-pi/.prettierrc.yaml", \
    "--ignore-path=/K-pi/.prettierignore", \
    "--plugin=@prettier/plugin-php" \
]
