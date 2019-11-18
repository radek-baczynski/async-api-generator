FROM asyncapi/generator

COPY templates /generator/templates

ENTRYPOINT ["node", "/asyncapi/cli", "-t", "/generator/templates", "-o", "/generated"]
