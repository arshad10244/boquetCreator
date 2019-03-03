# Bouquet Creator

**Bouquet Creator** is a PHP CLI based script which creates bouquet by utilizing bouquet specs and list of flowers. If bouquet has extra space left, it randomly adds random quantities of available bouquets.
The script uses OOP Structure and is written in core php. It uses composer for auto-loading classes with namespaces via psr-4. I've used loops instead of array_map as it seems loops work faster on certain arrays compared to array_map. The details of files are below

## Requirements
PHP 5.6+ with CLI support and it must be present in your PATH variable

### Usage

**On Windows Environment**

 1. Clone the repository in your project directory
 2. Navigate to the folder through shell
 3. Run the script through command line :

```
php shell.php < C:/path/to/yourfile.txt
```    

**On Linux Environment**
1. Clone the repository in your project root directory.
2. Navigate to the folder through shell
3. Run the script piping method :

```
cat /path/to/yourfile.txt | php shell.php
```

**On Docker Container without building image**
1. Clone the repository in your project root
2. Open Terminal and navigate to the root path of project
3. Run the following command

```
cat path/to/your/file.txt | docker run -i --rm -v $(pwd):/app -w /app php:cli php shell.php
```

**On Docker Container by building image**
1. Clone the repository in your project root
2. Open Terminal and navigate to the root path of project
3. Run the following command to build the image

```
 docker-compose build
``` 
Once the image is built, run the following command to execute the script:

```
cat path/to/your/file.txt | docker run -i --rm -v $(pwd):/app -w /app bloomon_app php shell.php
```


>Note: The file path must be an absolute path in all environments unless the input file is also located in your projects root path. 

>On first run on Docker, the process may take longer as it depends on php:cli image.


## File Structure

### App Entry Path

**shell.php** in the root directory is the entry path to the app

### Models

***/models*** contains all the models being used in the app.

**Bouquets.php** All properties of Bouquet is stored in this model.

**Flowers.php** All properties of Flowers are stored in this model

### Interfaces

We are using Interfaces to handle dependency Injection. Container can be used if the application is extended further.

**/interfaces** contains all interfaces

**Flowers.php** Interface used by Flowers model and injected in Bouquets model.

## Todo
1. When boquet has extra space, random flowers are added. However this should be slighly different
and should add the unused flowers first.
2. Creating a shell/batch file to run shell.php automatically
3. Adding support in shell.php to accept arguments and run in interactive mode where user is able to select an option
or provide file path.
