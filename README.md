# web_crawl

This application is used scrape the URL, write into the text file and matched string count from websites


#Installation

Composer URL is below: https://getcomposer.org/download/

1) After installing composer 
2) Download the repository
3) Navigate to the downloaded location
4) Open command terminal and enter command "composer install"

#Execution
1) Navigate to project folder
2) open command terminal
3) Type the below command to execute the application

  php index.php --site="https://www.xxxxx.com" --sites="website_list.txt" --match="matching strings|matching strings" --matches="matching_strings.txt"

#Sample Output files
1) response.json -> output json written with url status, matched string and its count
2) website_list.txt -> list of url scraped from the website
3) matching_strings.txt -> matched string



