# Line Endings Processor

[![Build Status](https://travis-ci.com/keboola/processor-line-endings.svg?branch=master)](https://travis-ci.com/keboola/processor-line-endings)

The processor takes a file and converts Windows (CRLF) and Mac (CR) to unix type line endings (LF).

# Usage

The processor takes all input files and tables, converts line endings and stores the results as output files or tables.
This means that binary files will get broken, use after decompressing.
The processor takes no parameters. Manifest files are copied unchanged.

```
{
    "definition": {
        "component": "keboola.processor-line-endings"
    }
}
```

## Development

Clone this repository and init the workspace with following command:

```
git clone https://github.com/keboola/processor-line-endings
cd processor-line-endings
docker-compose build
docker-compose run --rm dev composer install --no-scripts
```

Run the test suite using this command:

```
docker-compose run --rm dev composer tests
```
 
# Integration

For information about deployment and integration with KBC, please refer to the [deployment section of developers documentation](https://developers.keboola.com/extend/component/deployment/) 

## License

MIT licensed, see [LICENSE](./LICENSE) file.
