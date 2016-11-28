#!/usr/bin/python
import sys
import Adafruit_DHT
import time
import pymongo
import datetime


def main():
  db = connectMongo()
  measure_interval = 3; # 5 mins
  #hums = db.hum
  #temps = db.temp

  #entry_num_hum = hums.count()+1;
  #entry_num_temp = temps.count()+1;

  while True:
    #dto = datetime.now(timezone('UTC'))
    #dto_pacific = dto.astimezone(timezone('US/Pacific')) #.localize(dto)
    #dts = datetime.strftime(dto_pacific,"%Y-%m-%d %H:%M:%S")

    humidity, temperature = Adafruit_DHT.read_retry(11, 4)
    temperature = (int(temperature)*1.8)+32

    print humidity
    print temperature
    #hum_dict = {'time': dts, 'val': humidity, 'num': entry_num_hum }
    #temp_dict = {'time': dts, 'val': temperature, 'num': entry_num_temp }

    #hums.insert_one(hum_dict)
    #temps.insert_one(temp_dict)

    #entry_num_hum += 1;
    #entry_num_temp += 1;

    time.sleep(measure_interval);

def connectMongo():
  connection = pymongo.MongoClient("mongodb://admin:admin@ds019926.mlab.com:19926/qpdemo")
  db = connection.qpfallteam7
  return db

if __name__ == "__main__":
  main()

