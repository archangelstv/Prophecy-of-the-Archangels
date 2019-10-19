AirtestIDE Selenium Plugin
===========================

This plugin uses AirtestIDE to facilitate webpage's automation based on Selenium.

[Get started with AirtestIDE](http://airtest.netease.com/)

Since [Selenium](https://github.com/SeleniumHQ/selenium) is quire mature in web's automation, we have no reason to re-invent it. This plugin takes advantage of both AirtestIDE and Selenium to help developers do automation.


### Setup

1. ```pip install -r requirements.txt```
1. Upgrate chrome to latest version
1. Start chrome for remote debugging
```
> ./chrome.exe --remote-debugging-port=9222
```
1. Start recording selenium code in python using [chrome devtools-protocol](https://chromedevtools.github.io/devtools-protocol/). Reference code: [tests/airtest-chrome.py](./tests/airtest-chrome.py)
1. Run selenium code in chrome. Reference code: [tests/UntitledTestCase.py](./tests/UntitledTestCase.py)
```
options = webdriver.chrome.options.Options()
options.add_experimental_option("debuggerAddress", "127.0.0.1:9222");
driver = webdriver.Chrome(chrome_options=options)
driver.get("https://github.com/fate0/pychrome")
driver.find_element_by_link_text("pychrome").click()
```