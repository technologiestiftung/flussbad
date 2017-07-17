/**
 * Turbidity sensor
 * Reference: https://www.dfrobot.com/wiki/index.php/Turbidity_sensor_SKU:_SEN0189
 * 
 */

void setup() {
  Serial.begin(9600);
}
void loop() {
  int sensorValue = analogRead(A0);
  float voltage = sensorValue * (5.0 / 1024.0); // Convert the analog reading (which goes from 0 - 1023) to a voltage (0 - 5V):
  Serial.print(voltage);
  Serial.println(" V");
  delay(1000);
} 
