import sys 
import Adafruit_DHT
import time
import pymongo

##should we put everything below in a function main() as called at the end??

def main():

   db = connectMongo()
   measure_interval = 300; # 5 mins
   hums = db.hum
   temps = db.temp

   entry_num_hum = hums.count()+1;
   entry_num_temp = temps.count()+1;

# Try to grab a sensor reading.  Use the read_retry method which will retry up
# to 15 times to get a sensor reading (waiting 2 seconds between each retry).

   while True:

      humidity, temperature = Adafruit_DHT.read_retry(11, 4)
      temperature = temperature * 9/5.0 + 32

      hum_final = {'time': dts, 'val': humidity, 'num': entry_num_hum }
      temp_final = {'time': dts, 'val': temperature, 'num': entry_num_temp }
  
      hums.insert_one(hum_final)
      temps.insert_one(temp_final)

      entry_num_hum += 1;
      entry_num_temp += 1;

      time.sleep(measure_interval);

def connectMongo():
  connection = pymongo.MongoClient("mongodb://admin:admin@ds019746.mlab.com:19746/qpfallteam7")
  db = connection.qpfallteam7
  return db

if __name__ == "__main__":
  main()
