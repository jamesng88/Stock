#!/usr/bin/env python
# coding: utf-8

from selenium import webdriver
from selenium.webdriver.common.by import By 
from selenium.webdriver.common.keys import Keys 
from selenium.webdriver.chrome.options import Options
import sys
from datetime import datetime
import pymysql

#轉換日期格式
def date_conv(date_string):
    return datetime.strptime(date_string, "%d %b %Y").date()

phpRequest = "tm"#sys.argv[1]

connection = pymysql.connect(host="localhost", port=3307, user="root", passwd="", database="finance")
cursor = connection.cursor()
options = Options()
options.chrome_executable_path = r"C:\Users\ASUS\Desktop\chrome automatic\chromedriver-win64\chromedriver.exe"

#建立 Driver 物件實體，用 code 操作 Chrome
driver = webdriver.Chrome(options=options)
target_web = "https://www.klsescreener.com/v2/"
target_input_class = "searchquote"
target_input_btn = "btn btn-light"
driver.get(target_web)
searchInput = driver.find_element(By.ID, target_input_class)
searchInput.send_keys(phpRequest)
driver.implicitly_wait(1)#seconds
searchInput.send_keys(Keys.ENTER)

targetClass = "gsc-table-result"
searchLists = driver.find_element(By.CLASS_NAME, targetClass) #get the first result from the search result list
result = searchLists.find_element(By.TAG_NAME, "a") #get the href
stockPage = result.get_attribute("href")
stockPage = stockPage+"#dividends"
driver.get(stockPage)

#get company detail
stockid = driver.find_element(By.XPATH, ("//*[@id='page']/div[2]/div[1]/div[1]/div[1]/div[1]/div[1]/div[1]/h5")).text #get company id
sql = "SELECT * FROM stock WHERE stockid = %s"
val = (stockid)
cursor.execute(sql, val)
latestDate= date_conv("31 DEC 1957")
if (not (cursor.fetchone())):
    name = driver.find_element(By.XPATH, ("//*[@id='page']/div[2]/div[1]/div[1]/div[1]/div[1]/div[1]/div[1]/h2")).text #get company name
    fullname = driver.find_element(By.XPATH, ("//*[@id='page']/div[2]/div[1]/div[1]/div[1]/div[1]/div[1]/span")).text #get company full name
    market = driver.find_element(By.XPATH, ("//*[@id='page']/div[2]/div[1]/div[1]/div[1]/div[1]/div[1]/small")).text #get company market 
    stocktype = driver.find_element(By.XPATH, ("//*[@id='page']/div[2]/div[1]/div[1]/div[1]/div[1]/div[1]/div[3]/span")).text #get company type
    sql = "INSERT INTO stock (stockid, stockname, fullname, stocktype, market) VALUES (%s, %s, %s, %s, %s)"
    val = (stockid, name, fullname, stocktype, market)
    cursor.execute(sql, val)
    connection.commit()

else:
    sql = "SELECT max(ex_date) FROM dividend WHERE stockid = %s" #find the latest data from DB
    val = stockid
    cursor.execute(sql, val)
    tmp = cursor.fetchone()
    if(tmp[0] != None):
        latestDate = tmp[0]

searchLists = driver.find_element(By.XPATH, ("//*[@id='dividends']/table/tbody"))
result = searchLists.find_elements(By.TAG_NAME, "tr")
for x in result:
    xresult = x.find_elements(By.TAG_NAME, "td")
    v4 = date_conv(xresult[3].text) #Ex Date
    if(latestDate <= v4):
        print("newest data")
        break
    v1 = date_conv(xresult[0].text) #Announced Date
    v2 = date_conv(xresult[1].text) #Financial Year
    v3 = xresult[2].text #Subject    
    v5 = date_conv(xresult[4].text) #Payment Date
    v6 = xresult[5].text #Amount
    sql = "INSERT INTO dividend (stockid, announced_date, finance_year, dividendsubject, ex_date, payment_date, amount)     VALUES (%s, %s, %s, %s, %s, %s, %s)"
    val = (stockid, v1, v2, v3, v4, v5, v6)
    cursor.execute(sql, val)
    connection.commit()

connection.close()
driver.close()
print("DONE")