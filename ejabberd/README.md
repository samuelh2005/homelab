# ejabberd — single-node Docker Compose setup

Based on the official [ejabberd Container docs](https://docs.ejabberd.im/CONTAINER/), using the
recommended `ghcr.io/processone/ejabberd` image (as opposed to the older `ejabberd/ecs` image —
see [Images Comparison](https://docs.ejabberd.im/CONTAINER/#images-comparison)).

## Layout

```
.
├── docker-compose.yml
├── conf/
│   └── ejabberd.yml      # ejabberd configuration (mounted read-only)
├── database/             # Mnesia spool files (persistent)
├── logs/                 # ejabberd.log, error.log, etc. (persistent)
└── upload/               # files uploaded via mod_http_upload (persistent)
```

## Before first run

1. **Set your domain and admin JID.** Edit `EJABBERD_MACRO_HOST` and
   `EJABBERD_MACRO_ADMIN` in `docker-compose.yml`, and change
   `REGISTER_ADMIN_PASSWORD` to something real. These override the
   `HOST`/`ADMIN` macros defined at the top of `conf/ejabberd.yml`.

2. **Fix volume ownership.** Inside the container ejabberd runs as
   `ejabberd:ejabberd`, UID:GID `9000:9000`. The bind-mounted host
   directories need to be writable by that account:

   ```bash
   mkdir -p database logs upload
   sudo chown -R 9000:9000 database logs upload
   ```

   (On Podman, use `podman unshare chown 9000:9000 ...` instead of `sudo`.)

3. **TLS certificate.** `conf/ejabberd.yml` points `certfiles` at
   `/opt/ejabberd/conf/server.pem`. The image auto-generates a
   self-signed cert there on first start if the file doesn't exist yet,
   which is fine for testing. For real deployments, replace it with a
   proper certificate (e.g. from Let's Encrypt, which `mod_acme` /
   the `/.well-known/acme-challenge` handler in the config can help with)
   and mount it read-only alongside `ejabberd.yml`.

## Start it

```bash
docker compose up -d
```

This registers the admin account automatically (via
`REGISTER_ADMIN_PASSWORD`) and, on every start, runs
`CTL_ON_START` to print the registered users and status — check with:

```bash
docker compose logs -f
```

## Next steps

- **WebAdmin:** browse to `https://<host>:5443/admin/` (or
  `http://<host>:5280/admin/` without TLS) and log in with the admin
  JID/password you set above.
- **Register more accounts:**
  ```bash
  docker exec -it ejabberd ejabberdctl register someuser example.com somepassword
  ```
- **Tail logs from inside the container:**
  ```bash
  docker exec -it ejabberd tail -f logs/ejabberd.log
  ```
- **Open an Erlang debug console:**
  ```bash
  docker exec -it ejabberd ejabberdctl debug
  ```
- **Connect an XMPP client** (Gajim, Conversations, etc.) to
  `example.com` on port `5222`, using the account you registered.

## Ports

| Port | Purpose |
|---|---|
| 5222 | XMPP client connections (c2s) |
| 5269 | XMPP server-to-server federation (only needed to federate with other servers) |
| 5280 | HTTP: WebAdmin, BOSH, uploads (plaintext) |
| 5443 | HTTPS: WebAdmin, REST API, CAPTCHA, OAuth, Websockets, BOSH (TLS) |

MQTT (`1883`) and STUN/TURN ports are commented out in `docker-compose.yml`
— uncomment them if you enable `mod_mqtt` / a STUN/TURN listener in
`conf/ejabberd.yml`.

## Stopping / removing

```bash
docker compose down        # stop and remove the container, keep data
docker compose down -v     # also remove named volumes (not used here — data lives in ./database etc.)
```

Your data (accounts, messages, uploads, logs) stays on disk in `database/`,
`upload/`, and `logs/` between restarts, since those are bind mounts.

## Scaling to a cluster later

This is a single-node setup. If you later want to cluster multiple ejabberd
containers, see the "Clustering Example" in the
[ejabberd Container docs](https://docs.ejabberd.im/CONTAINER/#clustering-example) —
it requires setting `ERLANG_NODE_ARG` and a shared `ERLANG_COOKIE` per node,
and joining nodes with `CTL_ON_CREATE=join_cluster ejabberd@<main>`.
