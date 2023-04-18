# ChatGPT-PHP-Interface

This repository contains a PHP-based web interface that allows users to generate professional sales emails and legal content descriptions using the OpenAI GPT-3.5 model.

## Requirements
- PHP 7.2 or higher
- An OpenAI API key

## Usage

1. Clone or download the repository.
2. Navigate to the root folder of the repository on your local machine and run the PHP built-in server using the following command:

    ```
    php -S localhost:8000
    ```
3. In a web browser, navigate to `localhost:8000/index.php` to access the application interface.

The interface allows users to generate either a sales email or legal content descriptions.

### Sales Email Generation
To generate a sales email:

1. Select `Generate Email` from the dropdown menu.
2. Enter the notes from the sales call or meeting in the input box.
3. Click on the `Generate` button.

The API will generate a professional sales email based on the input notes and display it in the `Output` section.

### Legal Content Generation
To generate legal content descriptions:

1. Select `Generate Content` from the dropdown menu.
2. Enter the practice area, location, and keywords in the input boxes.
3. Click on the `Generate` button.

The API will generate 3 descriptions of interesting legal cases based on the input parameters and display them in the `Output` section.

## Files

### `index.php`

This file contains the HTML, CSS, and JavaScript code for the web interface.

### `generate_email.php`

This file contains the PHP code to generate a sales email using the OpenAI GPT-3.5 model. It first validates the input parameters and then sends a request to the OpenAI API to generate the email. The generated email is then returned and displayed in the `Output` section of the interface.

### `generate_content.php`

This file contains the PHP code to generate legal content descriptions using the OpenAI GPT-3.5 model. It first validates the input parameters and then sends a request to the OpenAI API to generate the descriptions. The generated descriptions are then returned and displayed in the `Output` section of the interface.

## API Key

To use this application, you will need to include your OpenAI API key in your environment variables. You can do this by adding the following line to your `.bash_profile` file:

```
export OPENAI_API_KEY=YOUR_API_KEY_HERE
```

Replace `YOUR_API_KEY_HERE` with your actual OpenAI API key. Once you have saved your changes, be sure to source your `.bash_profile` file so that your environment variables are updated.

## Disclaimer

This project is for educational purposes only and is not intended for use in production environments. Use of the OpenAI API is subject to the OpenAI API Terms of Service.
