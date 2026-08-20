#!/bin/bash
# Dev API server launcher (leafy-owned; prod-parity URL shape: /api/...)
exec /home/leafy/.pondok/php/bin/php -S 0.0.0.0:8081 \
  -t /home/leafy/projects/pondokhuda /home/leafy/projects/pondokhuda/dev_router.php