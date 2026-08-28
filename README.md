# homelab
Configurations for my homelab


## Kamailio DB setup

Set `DBHOST` in `kamctlrc` to your machine's IP address, then run the following command to create the Kamailio database and user:

`docker run -it --rm --entrypoint /bin/bash -v ./kamailio/:/etc/kamailio/:ro --env-file .env  ghcr.io/kamailio/kamailio:6.1.4-trixie`

Run command `kamdbctl create` to create the database and tables. This command will ask for the root password.

Use `mysql -u root -h <YOUR_IP_ADDRESS> -p` to connect to the database and grant the necessary privileges to the Kamailio user. For example:

```
GRANT ALL PRIVILEGES ON <kamailio_db_name>.* TO '<kamailio_db_user>'@'%' IDENTIFIED BY '<kamailio_db_password>';
FLUSH PRIVILEGES;
```

Replace `<kamailio_db_name>`, `<kamailio_db_user>`, and `<kamailio_db_password>` with the values from your `.env` file.
