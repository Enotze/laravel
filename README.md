```bash
docker-compose up -d --build
docker-compose exec app php -d memory-limit=-1 /usr/local/bin/composer install
docker-compose exec app artisan php artisan key:generate
docker-compose exec app artisan migrate
docker-compose exec app artisan ide-helper:generate #чтобы в ide не подсвечивало классы
curl localhost:8580 #проверка, что проект работает, должен вернуть html
```

## Minikube
### Чтобы работал registry при деплое в кубике
```bash
minikube start  --insecure-registry="192.168.99.1:5000
minikube addons enable registry
minikube ip 
# выводит к примеру 192.168.49.2
sudo gedit ~/.minikube/machines/minikube/config.json
# находим HostOptions - EngineOptions - InsecureRegistry и пишем в массив
# вместо 192.168.99.1:5000
# это 192.168.49.2:5000
minikube start
# теперь в Deployment можно указать к примеру 'image: 192.168.49.2:5000/nginx:base'
```
Чтобы проверить, что registry вообще работает, выполните: 

`curl http://192.168.49.2:5000/v2/`

в ответе должно быть `{}`

### Чтобы можно было пулить и пушить image в registry из локальной машины
`sudo gedit /etc/docker/daemon.json`

Прописать
```
{
  "insecure-registries" : ["192.168.49.2:5000"]
}
```
Перезапустить docker `sudo service docker restart`. 
Теперь можно пушить созданные локально image в registry 
```
docker build -t 192.168.49.2:5000/nginx:base -f ./deployment/dockerfiles/gitlab/nginx/Dockerfile ./deployment/dockerfiles/gitlab/nginx
docker push 192.168.49.2:5000/nginx:base
```

### Консольные команды для создания образов, которые после используются при деплое в kubernetes
```
docker image rm 192.168.49.2:5000/app:base
docker image rm 192.168.49.2:5000/nginx:base
docker build -f ./deployment/dockerfiles/gitlab/php/Dockerfile -t 192.168.49.2:5000/app:base .
docker build -f ./deployment/dockerfiles/gitlab/nginx/Dockerfile -t 192.168.49.2:5000/nginx:base .
docker push 192.168.49.2:5000/app:base
docker push 192.168.49.2:5000/nginx:base

docker image rm 192.168.49.2:5000/app:base && docker image rm 192.168.49.2:5000/nginx:base && docker build -f ./deployment/dockerfiles/gitlab/php/Dockerfile -t 192.168.49.2:5000/app:base . && docker build -f ./deployment/dockerfiles/gitlab/nginx/Dockerfile -t 192.168.49.2:5000/nginx:base . && docker push 192.168.49.2:5000/app:base && docker push 192.168.49.2:5000/nginx:base
```

### Удаляет pod и deployment приложения и создает заново
```
k replace --force -f ./deployment/k8s/app-deployment.yaml 
```
