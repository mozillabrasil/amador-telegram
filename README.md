## Amador Telegram Bot

[pt-br]

Este é um bot feito para dedicar MozLove em algum grupo de telegram.

O intuito é ter a possibilidade de demonstrar que alguém do grupo mandou bem, ou não, sobre alguma ação.

Para usá-lo basta enviar no grupo `@nomedousuario ++` (que irá adicionar MozLove) ou `@nomedousuario --` (que irá retirar MozLove).

A ideia é diversão e não promover ou punir algum membro do grupo.

### Como contribuir

#### Pré requisitos

* Git
* PHP 5.6+
* Ngrok
* Docker *
* Docker Compose *

OBS: Docker e Docker Compose não é obrigatório, porém irá auxiliar bastante o setup deste projeto.

#### Como Iniciar

##### No Telegram

* Procure pelo `BotFather` na aba de pesquisa
* Digite `/newbot`
* Coloque um nome que termine com bot. Ex: "amadorbot"
* Copie o token que ele irá te passar e cole no local indicado no arquivo `config.php.example`
* Altere o nome do arquivo `config.php.example` para `config.php`

##### Com Docker

* `cd docker`
* `docker-compose up --build` se desejar, rode detacado `docker-compose up --build -d`
* `docker exec -it php composer update`
* Em seu navegador acesse `localhost:8000` e clique na aba Status
* Você encontrará a URL que o telegram irá utilizar para rodar o BOT
* Copie a URL e abra outra aba
* Acesse `https://api.telegram.org/bot[token fornecido pelo botfather]/setwebhook?url=[url copiado no passo anterior]`
* Agora seu bot deve funcionar. Faça um teste enviando `@nomedeusario ++`

[en]

This bot was made to send MozLove in some telegram group.

To use it on the group you just need to send `@username ++` (that gonna add MozLove) or `@username --` (that gonna remove MozLove).

This idea is to having fun, not to improve or punish some group member.

### Get involved

#### Prerequisites

* Git
* PHP 5.6+
* Ngrok
* Docker *
* Docker Compose *

PS: Docker and Docker Compose isn't required, but it'll make you setup this project faster.

#### How to get start

##### On Telegram

* Search for `BotFather`
* Send `/newbot`
* Use a name that ends with bot, like "lovebot"
* Copy the token and paste on the indicate spot at `config.php.example`
* Rename this file to `config.php`

##### With Docker

* `cd docker`
* `docker-compose up --build` if do you want, run it detached `docker-compose up --build -d`
* `docker exec -it php composer update`
* In your browser, access `localhost:8000` and click in Status
* Here, you should see an URL. It'll be used by telegram to run you BOT.
* Copy this URL and open another tab.
* Access `https://api.telegram.org/bot[botfather token]/setwebhook?url=[URL copied on the last step]`
* Now your BOT should works. Try it sending `@username ++`