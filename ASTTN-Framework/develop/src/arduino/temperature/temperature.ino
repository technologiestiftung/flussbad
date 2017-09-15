// Reads the temperature from one DS18B20 sensor

#include <OneWire.h>
#include <DS18B20.h>

// 1-Wire bus Arduino pin
const byte ONEWIRE_PIN = 3;

// Sensor address
// EXAMPLE:
// byte sensorAddress[8] = {0x28, 0xFF, 0xBB, 0xF4, 0xA1, 0x16, 0x4, 0x72};
byte sensorAddress[8];

// 1-Wire object
OneWire onewire(ONEWIRE_PIN);
// DS18B20 sensors object
DS18B20 sensors(&onewire);

void setup() {
  // Serial port setup
  while(!Serial);
  Serial.begin(9600);

  // search DS18B20 sensorAdress
  while(onewire.search(sensorAddress))
  {
    Serial.println("next");
    if (sensorAddress[0] != 0x28)
      continue;
      
    if (OneWire::crc8(sensorAddress, 7) != sensorAddress[7])
    {
      Serial.println(F("1-Wire bus connection error!"));
      break;
    }

    //print found sensorAdress
    for (byte i=0; i<8; i++)
    {
      Serial.print(F("0x"));
      Serial.print(sensorAddress[i], HEX);
      
      if (i < 7)
        Serial.print(F(", "));
    }
    Serial.println();
  }
  
  // DS18B20 sensors setup
  sensors.begin();
}

void loop() {
  // Requests sensor for measurement
  sensors.request(sensorAddress);
  
  // Waiting (block the program) for measurement reesults
  while(!sensors.available());
  
  // Reads the temperature from sensor
  float temperature = sensors.readTemperature(sensorAddress);
  
  // Prints the temperature on Serial Monitor
  Serial.print(temperature);
  Serial.println(F(" 'C"));
}
