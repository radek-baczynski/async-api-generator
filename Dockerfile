FROM asyncapi/generator

COPY templates /generator/templates

CMD ["ag", "/doc/async-api-doc.yaml", "html", "-t", "/generator/templates", "-o", "/generated"]
