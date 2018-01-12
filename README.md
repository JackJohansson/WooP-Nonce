# WooP Nonce

**WooP-Nonce is a composer package that can be used to interact with WordPress nonces in an OOP way.**

------------------------------------------------------------------------------------------------------
#### Table of Content

- [About the project](#about-the-project)
- [Quick look at the features](#quick-look-at-the-features)
- [How to use](#how-to-use)
  - [Classes](#classes)
  - [Arguments](#arguments)
  - [Methods](#methods)
  - [Sample usage](#sample-usage)
- [How to install](#how-to-install)
- [Requirements](#requirements)
- [Licence](#licence)
------------------------------------------------------------------------------------------------------
## About the project

WordPress itself can pretty much handle every feature represented int this project, but they are all functional. In order to demonstrate an OOP version of nonces, we can use WooP-Nonce.

## Quick look at the features

By using WooP-Nonce, you can generate nonces, verify them and check referers. You can modify the nonce options, generate multiple nonces at once using random actions, and more. For the full list of features, read the next section.

# How to use

 WooP-Nonce provides two classes. One class is used to handle the nonces, and one is used to set the options.

## Classes

Nonce: The `Nonce` class handles generating and verifying the nonces and their referers. This class is defined under the` WoopNonce` namespace.

NonceOptions: The NonceOptions class is held in charge of overwriting the default nonce options. it internally uses WordPress filters to override these values.

## Arguments

When initializing these classes and calling their methods, an array of arguments can be passed to them or the methods. These arguments are fully optional. Each of the `Nonce` class's methods have their own arguments. However, the `NonceOptions` class accepts an array of 2 parameters:

- `nonce_life`: This is the lifetime of the nonce, controlled via the `nonce_life` filter. It can accepts either an integer number ( in seconds ) or a WordPress time constant.
- `message`: The message you want to show when the `wp_nonce_ays()` function is called. Accepts a single string.

## Methods

The `Nonce` class provides multiple methods to deal with the nonces. Here's a complete list of the methods:

- `GenerateNonce()`: This method will generate nonces based on the passed arguments. The full arguments are as follows:

  - `type`: You can set the type of the nonce you want to create. The supported types are 'field', 'url' and 'plain'.
    - `field`: By setting the nonce type to field, the output will be a hidden nonce field that can be used in a form. You can control the referer field in the referer paremeter.
    - `url`: If you are trying to nonce a URL, you can set the type to url, and pass a valid URL to the 'url' parameter.
    - `plain`: This is for advanced users that only want the nonce string itself.
  - `action`: You can pass your desired action to the class. If no action is passed, the default -1 action will be used. If you wish to use a random action, you can pass the 'rand' string. This is useful when you want to create a number of nonces.
  - `name`: The name to be used for outputting the hidden input in the 'field' type. If left blank, the default '\_wpnonce'' will be used.
  - `referer`: If you don't want to output the referer field in the 'field' type, you can set this prameter to false.
  - `object`: Setting this parameter to true will result in the output being an object. This way you have access to all the nonce's informations, including name, action, referer, and the nonce string itself.
  - `count`: If you are trying to generate a number of nonces, you can set this paremeter to simply output multiple nonces. This only works on plain nonce type.
  - `url`: If trying to generate a URL nonce, you should pass a valid URL to this parameter. An invalid parameter will result in a null output.

- `VerifyNonce()`: This method is used to verify an output nonce. It accepts a mandatory `$nonce` parameter, and an optional `$action` paremeter.
- `VerifyReferrer()`: A method to verify the AJAX referer. Accepts 3 optional parameters, `$action`, `( boolean ) $query_arg`, and `( boolean ) $die`.
- `VerifyAdminReferer()`: This method serves the similiar purpose as the previous once, but is used to check the referer passed in the admin screens. Accepts 2 optional parameters, `$action` and `( boolean ) $query_arg`
- `GetReferer()`: This method gets the hidden referer field that is used in creating nonce fields for forms.

The `NonceOptions` class provides 2 methods, that can be used to retrieve the value of current options. These methods are:

 - `ReturnNonceLife()`: Returns the current lifetime of the nonces.
 - `ReturnTranslation()`: Returns the current translation string that is output by the `wp_nonce_ays()` function.

## Sample Usage
We're going to have a look at the examples of how to use the above methods. In the first example, we will generate a simple nonce string:

```php
$woop_nonce = new \WoopNonce\Nonce();

// Create a simple nonce string
echo $woop_nonce->GenerateNonce();
```

The above code will result in a nonce string being output.

Now, let's try and make a bunch of nonces. We're going to generate 5 nonces:

```php
$woop_nonce = new \WoopNonce\Nonce();

// Create 5 nonces
$nonces     = $woop_nonce->GenerateNonce( [ 'count' => 5 ] );

print_r ( $nonces );
```

Since we are creating 5 nonces, the output will be an array, similiar to this:

```php
Array
(
   [0] => Array
       (
           [nonce] => 50c2b1c3eb
           [action] => KpFobPqK6fFc
       )

   [1] => Array
       (
           [nonce] => 5fd5eadd53
           [action] => tQVXT7rv5tSg
       )

   [2] => Array
       (
           [nonce] => a4ce41ec8a
           [action] => DFsFBnztGMQo
       )

   [3] => Array
       (
           [nonce] => 99992f9a62
           [action] => dZcCklvcLWGS
       )

   [4] => Array
       (
           [nonce] => 33965df2db
           [action] => K0hWeTdqnqVN
       )

)
```

The actions are also generated randomly. Now, time to create an object of 3 nonces, with random actions:


```php
$woop_nonce = new \WoopNonce\Nonce();

// Create 3 nonces
$nonces     = $woop_nonce->GenerateNonce( [ 'type' => 'plain', 'count' => 3, 'object' => true ] );

print_r ( $nonces );
```

The result would be an object of 3 nonces, along with their full details:

```php
stdClass Object
(
   [0] => stdClass Object
       (
           [nonce] => 338dad7456
           [action] => mAWQqq3Jknol
           [referer] => /current-referer/
       )

   [1] => stdClass Object
       (
           [nonce] => 2bfefc0964
           [action] => iLnmk6zCMAf3
           [referer] => /current-referer/
       )

   [2] => stdClass Object
       (
           [nonce] => b9fed528b3
           [action] => 70fTiTxWWSrM
           [referer] => /current-referer/
       )

)
```

Here we'll have an example of how to verify a nonce. To do so, we create a nonce and verify it:

```php
$woop_nonce = new \WoopNonce\Nonce();

// Create a simple nonce string
$nonce      = $woop_nonce->GenerateNonce();

echo $woop_nonce->VerifyNonce( $nonce );
```

Which will validate, and an integer number of 1 will be passed as the output.

The rest of the methods have a pretty self explanatory usage, and are very similiar to their representitive WordPress functions.

For the final example, we're going to set the nonce options using the `NonceOptions` class. This class should be initiated as soon as the `init` action hook. Let's alter the lifetime and the message:

```php
add_action( 'init', '\WoopNonce\initiate_nonces_options' );
function initiate_nonce_options() {

	$args = [
		'nonce_life' => 5 * HOUR_IN_SECONDS,
		'message'    => 'Oh No!',
	];

	new NonceOptions( $args );
}
```

Which will set the lifetime of the nonces to 5 hours, and the message to `Oh No!`.

# How to install

Install the composer package manager and require the package in the 1.0.1 version:

`composer require johansson/woop-nonce`

# Requirements

- PHP version 5.6 or higher
- Composer package manager
- A few minutes to spare

# Licence

This package is licenced under the MIT.
