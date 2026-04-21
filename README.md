# homelab
Configurations for my homelab


## Firewall setup:

```
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 25565/tcp
ufw allow 25565/udp
ufw allow 19132/udp
ufw enable
```