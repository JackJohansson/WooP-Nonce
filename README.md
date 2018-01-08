# WooP Nonce
WooP-Nonce is a composer package that can be used to interact with WordPress nonces in an OOP way.

###### Table of Content

- About the project
- Quick look at the features
- How to use
  - Classes
  - Arguments
  - Methods
  - Sample usage
- How to install
- Requirements
- Licence

## About the project

WordPress itself can pretty much handle every feature represented int this project, but they are all functional. In order to demonstrate an OOP version of nonces, we can use WooP-Nonce.

## Quick look at the features

By using WooP-Nonce, you can generate nonces, verify them and check referers. You can modify the nonce options, generate multiple nonces at once using random actions, and more. For the full list of features, read the next section.

# How to use

 WooP-Nonce provides two classes. One class is used to handle the nonces, and one is used to set the options.

###### Classes

Nonce: The Nonce class handles generating and verifying the nonces and their referers. This class is defined under the WoopNonce namespace.

NonceOptions: The NonceOptions class is held in charge of overwriting the default nonce options. it internally uses WordPress filters to override these values.

###### Arguments

When initializing these classes, an array of arguments can be passed to them. These arguments are fully optional. The arguments for the Nonce class are as follows:

1. type: You can set the type of the nonce you want to create. The supported types are 'field', 'url' and 'plain'.
   - field: By setting the nonce type to field, the output will be a hidden nonce field that can be used in a form. You can control the referer field in the referer paremeter.
   - url: If you are trying to nonce a URL, you can set the type to url, and pass a valid URL to the 'url' parameter.
   - plain: This is for advanced users that only want the nonce string itself.
2. action: You can pass your desired action to the class. If no action is passed, the default -1 action will be used. If you wish to use a random action, you can pass the 'rand' string. This is useful when you want to create a number of nonces.
3. name: The name to be used for outputting the hidden input in the 'field' type. If left blank, the default '\_wpnonce'' will be used.
4. referer: If you don't want to output the referer field in the 'field' type, you can set this prameter to false.
5. object: Setting this parameter to true will result in the output being an object. This way you have access to all the nonce's informations, including name, action, referer, and the nonce string itself.
6. count: If you are trying to generate a number of nonces, you can set this paremeter to simply output multiple nonces. This only works on plain nonce type.
7. url: If trying to generate a URL nonce, you should pass a valid URL to this parameter. An invalid parameter will result in a null output.

The NonceOptions class accepts two parameters:

1. nonce_life: This is the lifetime of the nonce, controlled via the 'nonce_life' filter. It can accepts either an integer number ( in seconds ) or a WordPress time constant.
2. message: The message you want to show when the `wp_nonce_ays()` function is called. Accepts a single string.

###### Methods
