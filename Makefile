build:
	docker build . -t radekb/async-api-generator

push:
	docker push radekb/async-api-generator

generate:
	rm -rf $(dest)/*
	docker run --rm -it -v $(doc):/doc/async-api-doc.yaml -v $(dest):/generated radekb/async-api-generator

publish:
	aws s3 cp generated s3://async-docs.dq.docplanner.io/$(app)/$(branch)/$(doc) --recursive
