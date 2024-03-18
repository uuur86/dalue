# Dalue Mapper Class Usage

The `Mapper` class under the `Dalue` namespace offers a powerful and flexible method for data transformation and restructuring. This class allows you to create a new structure from a given data set, optionally performing transformations on the data in the process.

## Getting Started

Before you begin using the `Mapper` class, make sure that the library containing the `StrObj\StringObjects` class is installed. The `Mapper` class uses the functionalities provided by this class for data access and manipulation.

## Example Usage

Below is an example of how to use the `Mapper` class:

```php
use Dalue\Mapper;
use StrObj\StringObjects;

// Sample data set
$data = new StringObjects([
    'user' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'details' => [
            'age' => 30,
            'city' => 'New York'
        ]
    ]
]);

// Defining the data structure we want to transform into.
$paths = [
    'userID' => '@data/user/id',
    'userName' => '@data/user/name',
    'userDetails' => [
        'userAge' => '@data/user/details/age',
        'userCity' => '@data/user/details/city'
    ]
];

// Using the Mapper::map function to perform the transformation.
$mappedData = Mapper::map($data, $paths);

print_r($mappedData);
```

## LICENSE

GPL-3.0-or-later

## AUTHOR

Uğur Biçer - @uuur86

## CONTRIBUTING

If you want to contribute to this project, you can send pull requests. We expect all contributors to follow our [Code of Conduct](CONTRIBUTING.md).

## CONTACT

You can contact me via email: contact@codeplus.dev

## BUGS

You can report bugs via github issues.

## SECURITY

If you find a security issue, please report it via email: contact@codeplus.dev

## DONATE

If you want to support me, you can donate via github sponsors: <https://github.com/sponsors/uuur86>

## SEE ALSO

- [uuur86/strobj]( https://github.com/uuur86/strobj ) - PHP String Objects
- [uuur86/wpoauth]( https://github.com/uuur86/wpoauth ) - Wordpress OAuth2 Client
- [@codeplusdev]( https://github.com/codeplusdev ) - Codeplus Development
