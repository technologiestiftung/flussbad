#define SERIAL_DEBUG_ENABLED 1    // 1 for serial debug informations

#define DATASIZE DATASIZE_PRTCL + DATASIZE_FLOAT + DATASIZE_FLOAT

#define DATASIZE_PRTCL  1    //1 Byte protocolversion
#define DATASIZE_FLOAT  7    //2 Byte sensortype, 4 Bytes float
#define DATASIZE_DOUBLE 10   //2 Byte sensortype, 8 Bytes float
#define DATASIZE_INT    7    //2 Byte sensortype, 4 Bytes int

#define PRTCL_VERSION 0x01
#define PRTCL_TEMP    0x0001
#define PRTCL_TURB    0x0007

#ifdef SERIAL_DEBUG_ENABLED 
  #define DebugPrint(...) Serial.print(__VA_ARGS__)
  #define DebugPrintln(...) Serial.println(__VA_ARGS__)
#else
  #define DebugPrint(...)
  #define DebugPrintln(...)  
#endif

//************************************************************************************
//**        inlcudes                                                                **
//************************************************************************************

//includes for LoRaWAN0
#include <lmic.h>
#include <hal/hal.h>
#include <SPI.h>

//includes for temperature
#include <OneWire.h>
#include <DS18B20.h>


//************************************************************************************
//**        globals                                                                 **
//************************************************************************************



//globales for TTN
// LoRaWAN NwkSKey, network session key
// This is the default Semtech key, which is used by the prototype TTN
// network initially.
static const PROGMEM u1_t NWKSKEY[16] = { 0x65, 0xCF, 0xBC, 0xBA, 0xEA, 0x70, 0xFB, 0x03, 0xD9, 0x8B, 0x92, 0xEB, 0x3B, 0x9D, 0xED, 0xAB };
//{ 0xAB, 0xED, 0x9D, 0x3B, 0xEB, 0x92, 0x8B, 0xD9, 0x03, 0xFB, 0x70, 0xEA, 0xBA, 0xBC, 0xCF, 0x65 };

// LoRaWAN AppSKey, application session key
// This is the default Semtech key, which is used by the prototype TTN
// network initially.
static const u1_t PROGMEM APPSKEY[16] = { 0x90, 0xF1, 0x50, 0xBB, 0x7F, 0x62, 0x5C, 0xF5, 0x61, 0x14, 0xE5, 0xBA, 0x1F, 0xC0, 0x0A, 0x1F };
//{ 0x1F, 0x0A, 0xC0, 0x1F, 0xBA, 0xE5, 0x14, 0x61, 0xF5, 0x5C, 0x62, 0x7F, 0xBB, 0x50, 0xF1, 0x90 };

// LoRaWAN end-device address (DevAddr)
// See http://thethingsnetwork.org/wiki/AddressSpace
static const u4_t DEVADDR = 0x26011EB9; // <-- Change this address for every node!

//globals for temperature
const byte ONEWIRE_PIN = 3;
byte temp_sensor_adress[8];
OneWire onewire(ONEWIRE_PIN);
DS18B20 temp_sensors(&onewire);

//globals for turbidity
byte turb_sensor_adress = A0;

static osjob_t sendjob;

// Schedule TX every this many seconds (might become longer due to duty
// cycle limitations).
const unsigned TX_INTERVAL = 30;

// Pin mapping
const lmic_pinmap lmic_pins = {
    .nss = 10,
    .rxtx = LMIC_UNUSED_PIN,
    .rst = 9,
    .dio = {2, 6, 7},
};

uint8_t mydata[DATASIZE];

//************************************************************************************
//**        setup & loop                                                            **
//************************************************************************************

void setup() {
  Serial.begin(9600);
  setup_temperature();
  setup_turbidity();
  setup_lorawan();
}

void loop() {
    os_runloop_once();
}


//************************************************************************************
//**        read sensors & build msg                                                **
//************************************************************************************

void build_msg()
{
    float temp = 0;
    float turb = 0;
    temp = read_temp();
    turb = read_turb();

    mydata[0] = PRTCL_VERSION;
    mydata[1] = (PRTCL_TEMP & 0xFF);
    mydata[2] = (PRTCL_TEMP >> 8);
    FtoLE(temp, &mydata[3]);
    mydata[7] = (PRTCL_TURB & 0xFF);
    mydata[8] = (PRTCL_TURB >> 8);
    FtoLE(temp, &mydata[9]);
}

//************************************************************************************
//**        setup sensors                                                           **
//************************************************************************************

void setup_temperature()
{
  // search DS18B20 sensorAdress
  while(onewire.search(temp_sensor_adress))
  {
    if (temp_sensor_adress[0] != 0x28)
      continue;
      
    if (OneWire::crc8(temp_sensor_adress, 7) != temp_sensor_adress[7])
    {
    	DebugPrintln(F("1-W err"));
      //DebugPrintln(F("1-Wire bus connection error!"));
      break;
    }
  }
  
  // DS18B20 sensors setup
  temp_sensors.begin();
}


void setup_turbidity()
{
}


//************************************************************************************
//**        read sensors                                                            **
//************************************************************************************

float read_temp(void)
{
  // Requests sensor for measurement
  temp_sensors.request(temp_sensor_adress);
  
  // Waiting (block the program) for measurement reesults
  while(!temp_sensors.available());
  
  // Reads the temperature from sensor
  return temp_sensors.readTemperature(temp_sensor_adress) + 273.15;  //°C to K
}


float read_turb()
{
  int sensorValue = analogRead(A0);
  return sensorValue * (5.0 / 1024.0); // Convert the analog reading (which goes from 0 - 1023) to a voltage (0 - 5V):
}


//************************************************************************************
//**        LoRaWAN                                                                 **
//************************************************************************************

void os_getArtEui (u1_t* buf) { }
void os_getDevEui (u1_t* buf) { }
void os_getDevKey (u1_t* buf) { }

void onEvent (ev_t ev) {
    switch(ev) {
        case EV_SCAN_TIMEOUT:
            DebugPrintln(F("SCAN_TIMEOUT"));
            break;
        case EV_BEACON_FOUND:
            DebugPrintln(F("BEACON_FOUND"));
            break;
        case EV_BEACON_MISSED:
            DebugPrintln(F("BEACON_MISSED"));
            break;
        case EV_BEACON_TRACKED:
            DebugPrintln(F("BEACON_TRACKED"));
            break;
        case EV_JOINING:
            DebugPrintln(F("JOINING"));
            break;
        case EV_JOINED:
            DebugPrintln(F("JOINED"));
            break;
        case EV_RFU1:
            DebugPrintln(F("RFU1"));
            break;
        case EV_JOIN_FAILED:
            DebugPrintln(F("JOIN_FAILED"));
            break;
        case EV_REJOIN_FAILED:
            DebugPrintln(F("REJOIN_FAILED"));
            break;
        case EV_TXCOMPLETE:
            DebugPrintln(F("TXCOMPLETE (includes waiting for RX windows)"));
            if(LMIC.dataLen) {
                // data received in rx slot after tx
                DebugPrint(F("Data Received: "));
                Serial.write(LMIC.frame+LMIC.dataBeg, LMIC.dataLen);
                DebugPrintln();
            }
            // Schedule next transmission
            os_setTimedCallback(&sendjob, os_getTime()+sec2osticks(TX_INTERVAL), do_send);
            break;
        case EV_LOST_TSYNC:
            DebugPrintln(F("LOST_TSYNC"));
            break;
        case EV_RESET:
            DebugPrintln(F("RESET"));
            break;
        case EV_RXCOMPLETE:
            // data received in ping slot
            DebugPrintln(F("RXCOMPLETE"));
            break;
        case EV_LINK_DEAD:
            DebugPrintln(F("LINK_DEAD"));
            break;
        case EV_LINK_ALIVE:
            DebugPrintln(F("LINK_ALIVE"));
            break;
         default:
            DebugPrintln(F("Unknown event"));
            break;
    }
}

void do_send(osjob_t* j){
  uint8_t i;
  // Check if there is not a current TX/RX job running
  if (LMIC.opmode & OP_TXRXPEND) {
    DebugPrintln(F("OP_TXRXPEND, not sending"));
  } else {
    // Prepare upstream data transmission at the next possible time.
    build_msg();

    #if(SERIAL_DEBUG_ENABLED == 1) 
    DebugPrint(F("mydata: "));
    for( i = 0; i < sizeof(mydata); i++) {
      DebugPrint(mydata[i], HEX);
      DebugPrint(" ");
    }
    DebugPrintln();
    #endif

    //LMIC_setTxData2(1, (char*) &cnter, sizeof(cnter), 0);
    LMIC_setTxData2(1, mydata, sizeof(mydata)-1, 0);
    //DebugPrint("Packet queued\nLMIC.freg: ");
    //DebugPrintln(LMIC.freq);
  }
  // Next TX is scheduled after TX_COMPLETE event.
}

void setup_lorawan() {

    // LMIC init
    os_init();
    // Reset the MAC state. Session and pending data transfers will be discarded.
    LMIC_reset();

    // Set static session parameters. Instead of dynamically establishing a session
    // by joining the network, precomputed session parameters are be provided.
    #ifdef PROGMEM
    // On AVR, these values are stored in flash and only copied to RAM
    // once. Copy them to a temporary buffer here, LMIC_setSession will
    // copy them into a buffer of its own again.
    uint8_t appskey[sizeof(APPSKEY)];
    uint8_t nwkskey[sizeof(NWKSKEY)];
    memcpy_P(appskey, APPSKEY, sizeof(APPSKEY));
    memcpy_P(nwkskey, NWKSKEY, sizeof(NWKSKEY));
    LMIC_setSession (0x1, DEVADDR, nwkskey, appskey);
    #else
    // If not running an AVR with PROGMEM, just use the arrays directly 
    LMIC_setSession (0x1, DEVADDR, NWKSKEY, APPSKEY);
    #endif

    // Set up the channels used by the Things Network, which corresponds
    // to the defaults of most gateways. Without this, only three base
    // channels from the LoRaWAN specification are used, which certainly
    // works, so it is good for debugging, but can overload those
    // frequencies, so be sure to configure the full frequency range of
    // your network here (unless your network autoconfigures them).
    // Setting up channels should happen after LMIC_setSession, as that
    // configures the minimal channel set.
    //LMIC_setupChannel(0, 868100000, DR_RANGE_MAP(DR_SF12, DR_SF7),  BAND_CENTI);      // g-band
    //LMIC_setupChannel(1, 868300000, DR_RANGE_MAP(DR_SF12, DR_SF7B), BAND_CENTI);      // g-band
    //LMIC_setupChannel(2, 868500000, DR_RANGE_MAP(DR_SF12, DR_SF7),  BAND_CENTI);      // g-band
    //LMIC_setupChannel(3, 867100000, DR_RANGE_MAP(DR_SF12, DR_SF7),  BAND_CENTI);      // g-band
    //LMIC_setupChannel(4, 867300000, DR_RANGE_MAP(DR_SF12, DR_SF7),  BAND_CENTI);      // g-band
    //LMIC_setupChannel(5, 867500000, DR_RANGE_MAP(DR_SF12, DR_SF7),  BAND_CENTI);      // g-band
    //LMIC_setupChannel(6, 867700000, DR_RANGE_MAP(DR_SF12, DR_SF7),  BAND_CENTI);      // g-band
    //LMIC_setupChannel(7, 867900000, DR_RANGE_MAP(DR_SF12, DR_SF7),  BAND_CENTI);      // g-band
    //LMIC_setupChannel(8, 868800000, DR_RANGE_MAP(DR_FSK,  DR_FSK),  BAND_MILLI);      // g2-band
    // TTN defines an additional channel at 869.525Mhz using SF9 for class B
    // devices' ping slots. LMIC does not have an easy way to define set this
    // frequency and support for class B is spotty and untested, so this
    // frequency is not configured here.


    // Disable link check validation
    LMIC_setLinkCheckMode(0);
    
    // Set data rate and transmit power (note: txpow seems to be ignored by the library)
//    LMIC_setDrTxpow(DR_SF7,14);
    LMIC_setDrTxpow(DR_SF12,14);

    // Start job
    do_send(&sendjob);
}



//************************************************************************************
//**        conversions                                                             **
//************************************************************************************

void FtoLE(float f, uint8_t* le)
{
  union u_tag 
  {
    byte b[4];
    float fval;
  } u;

  u.fval = f;
  le[0] = u.b[0];
  le[1] = u.b[1];
  le[2] = u.b[2];
  le[3] = u.b[3];
}
