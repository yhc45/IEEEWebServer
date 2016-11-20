import sys
import pymongo 
import Adafruit_DHT

##should we put everything below in a function main() as called at the end??

db = connectMongo()
measure_interval = 300; # 5 mins
hums = db.hum
temps = db.temp

entry_num_hum = hums.count()+1;
entry_num_temp = temps.count()+1;

# Parse command line parameters.
sensor_args = { '11': Adafruit_DHT.DHT11,
                '22': Adafruit_DHT.DHT22,
                '2302': Adafruit_DHT.AM2302 }
if len(sys.argv) == 3 and sys.argv[1] in sensor_args:
    sensor = sensor_args[sys.argv[1]]
    pin = sys.argv[2]
else:
    print('usage: sudo ./Adafruit_DHT.py [11|22|2302] GPIOpin#')
    print('example: sudo ./Adafruit_DHT.py 2302 4 - Read from an AM2302 connected to GPIO #4')
    sys.exit(1)

# Try to grab a sensor reading.  Use the read_retry method which will retry up
# to 15 times to get a sensor reading (waiting 2 seconds between each retry).
humidity, temperature = Adafruit_DHT.read_retry(sensor, pin)

hum_dict = {'val': humidity, 'num': entry_num_hum }
temp_dict = {'val': temperature, 'num': entry_num_temp }
  
hums.insert_one(hum_dict)
temps.insert_one(temp_dict)

entry_num_hum += 1;
entry_num_temp += 1;



# Un-comment the line below to convert the temperature to Fahrenheit.
# temperature = temperature * 9/5.0 + 32

# If you don't get a reading 
if humidity is not None and temperature is not None:
    print('Temp={0:0.1f}*  Humidity={1:0.1f}%'.format(temperature, humidity))
else:
    print('Failed to get reading. Try again!')
    sys.exit(1)

def connectMongo():
  connection = pymongo.MongoClient("mongodb://<dbuser>:<dbpassword>@ds019746.mlab.com:19746/qpfallteam7")
  db = connection.qpdemo
  return db

if __name__ == "__main__":
  main()
