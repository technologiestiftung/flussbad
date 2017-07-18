

/*
	init define the sensors
*/
INSERT INTO `lora`.`sensor_type` (`Name`, `unit`) VALUES ('temperature', 'K');
INSERT INTO `lora`.`sensor_type` (`Name`, `unit`) VALUES ('ultrasonic ranging', 'm');
INSERT INTO `lora`.`sensor_type` (`Name`, `unit`) VALUES ('humidity', 'g/m³');
INSERT INTO `lora`.`sensor_type` (`Name`, `unit`) VALUES ('pressure', 'Pa');
INSERT INTO `lora`.`sensor_type` (`Name`) VALUES ('turbidity');
INSERT INTO `lora`.`sensor_type` (`Name`) VALUES ('pH meter');



/*
	insert a new application to the db 

	administrative insert only by the TSB
*/
INSERT INTO `lora`.`application` (`app_id`) VALUES ('1312-test-app');
INSERT INTO `lora`.`application` (`app_id`) VALUES ('ab3_ultrasonic');


/* ------------------------------------------------------------ */

/*
	get the new device 
*/
INSERT INTO `lora`.`device` (`dev_id`) VALUES ('1312-dev01');
INSERT INTO `lora`.`device` (`dev_id`) VALUES ('1717-dev01');

INSERT INTO `lora`.`hardware` (`hardware_serial`) VALUES ('00CE1969FB0261DA');

INSERT INTO `lora`.`node` (`app_id`, `dev_id`, `hw_id`) VALUES ('1', '1', '1');

INSERT INTO `lora`.`data_rate` (`data_rate`) VALUES ('SF12BW125');

INSERT INTO `lora`.`metadata` (
    `server_time`,
    `frequency`,
    `coding_rate`,
    `modulation`,
    `data_rate_id`,
    `latitude`,
    `longitude`,
    `altitude`
) VALUES ('2017-07-12 18:20:14.27932437', '868.1', '4/5', 'LORA', '1', '52.504475', '13.315682', '45');


INSERT INTO `lora`.`gateway` (`gtw_id`) VALUES ('eui-1dee16aa26f490d7');

INSERT INTO `lora`.`gtw_metadata` (
	`gateway_id`,
    `timestamp`,
    `channel`,
    `rssi`,
    `snr`,
    `rf_chain`,
    `latitude`,
    `longitude`,
    `altitude`
) VALUES ('1', NULL, '0', '-121', '-17', '1', '52.5071', '13.3284', '48');

INSERT INTO `lora`.`sensor` (`node_id`, `sensor_type_id`) VALUES ('1', '1');
INSERT INTO `lora`.`sensor` (`node_id`, `sensor_type_id`) VALUES ('1', '5');
INSERT INTO `lora`.`sensor` (`node_id`, `sensor_type_id`) VALUES ('1', '6');

/*
	connect the metadata with all gtw_metadatas
*/
INSERT INTO `lora`.`rec_gtw` (`metadata_id`, `gateway_id`) VALUES ('1', '1');
