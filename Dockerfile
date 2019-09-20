FROM asyncapi/generator

COPY templates /generator/templates

CMD ["ag", "/doc/async-api-doc.yaml", "html", "-t", "/generator/templates", "-o", "/generated", "&&", "cp", "/doc/async-api-doc.yaml", "/generated/async-api-doc.yaml"]
