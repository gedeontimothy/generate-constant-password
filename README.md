# Generate Constant Password

## Description  
This PHP CLI tool generates a unique password based on a constant pattern using a keyword provided as an argument, a base password, and an infix defined in a configuration file.

## Installation  

1. Clone the project:  
   ```sh
   git clone <repository-url>
   cd <project-folder>
   ```

2. Configure the environment:  
   - Create an `env.php` file and define the following key:  
     ```php
     return [
         'password_infix' => 'InMiddle', // optional
     ];
     ```

3. Configure character values:  
   - Modify the `config.php` file to define values for each letter:  
     ```php
     return [
         'chars' => [
             'a' => 89,
             'b' => 87,
             'c' => 15,
             'd' => 39,
             // e - z...
         ],
     ];
     ```

## Usage  

1. Run the following command in a terminal:  
   ```sh
   php pass <keyword>
   ```

   Example with "Google":  
   ```sh
   php pass Google
   ```

2. Enter the base password when prompted:  
   ```
   Password:
   ```
   Example:
   ```sh
   php pass mybase123
   ```

3. The generated password will be displayed:  
   ```
   mybase123InMiddleGoogle12@
   ```

## Customization  
- **Infix**: Editable in `env.php`  
- **Character values**: Editable in `config.php`  

## License  
This project is licensed under the MIT License. See `LICENSE` for more information.