build:
	docker build . -t radekb/async-api-generator

push:
	docker push radekb/async-api-generator

generate:
	rm -rf $(dest)/*
	docker run --rm -it -v $(doc):/doc/async-api-doc.yaml -v $(dest):/generated radekb/async-api-generator
