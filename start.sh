#!/bin/zsh

set -e

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_DIR"

find_free_port() {
  local candidate="$1"
  while lsof -nP -iTCP:"$candidate" -sTCP:LISTEN >/dev/null 2>&1; do
    candidate=$((candidate + 1))
  done
  echo "$candidate"
}

if ! command -v docker >/dev/null 2>&1; then
  echo "未找到 Docker。请先安装 Docker Desktop： https://www.docker.com/products/docker-desktop/"
  exit 1
fi

if ! docker info >/dev/null 2>&1; then
  echo "正在启动 Docker Desktop..."
  open -a Docker 2>/dev/null || true

  echo "等待 Docker 引擎启动（最多 90 秒）..."
  ready=0
  for i in {1..45}; do
    if docker info >/dev/null 2>&1; then
      ready=1
      break
    fi
    sleep 2
  done

  if [[ "$ready" -ne 1 ]]; then
    echo "Docker 引擎仍未就绪。请打开 Docker Desktop 后重新运行："
    echo "  $PROJECT_DIR/start.sh"
    exit 1
  fi
fi

WORDPRESS_PORT="$(find_free_port "${WORDPRESS_PORT:-8080}")"
export WORDPRESS_PORT

echo "启动 WordPress 和数据库（端口 $WORDPRESS_PORT）..."
docker compose up -d

echo ""
echo "小尾巴 WordPress 已启动："
echo "  网站： http://localhost:$WORDPRESS_PORT"
echo "  后台： http://localhost:$WORDPRESS_PORT/wp-admin"
echo ""
echo "查看状态：docker compose ps"
echo "查看日志：docker compose logs -f wordpress"
