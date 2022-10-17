##
# Various helpful commands
##
.DEFAULT_GOAL := help

LOCALDATE := $(shell date +'%Y%m%d-%H%M')

-include .env

.PHONY: rebuild-caches
rebuild-caches: ## Clear Application cache
		@echo '🔧 make clear-app-cache'
		@echo ----------------------------------------------------------------
		${HOST_REMOTE_PHP_VERSION} ${HOST_REMOTE_PATH}/vendor/bin/drush cr
		@echo ----------------------------------------------------------------
		@echo '✨ Application caches have been rebuilt!'
		@echo


.PHONY: get-remote-db
get-remote-db: ## Dump remote drupal database locally
	@echo ----------------------------------------------------------------
	@echo '🔧 make get-remote-db'
	@echo ----------------------------------------------------------------
	make .exec-ssh-cmd CMD="mkdir -p ${HOST_REMOTE_PATH}/backups/db"
	make .exec-ssh-cmd CMD="${HOST_REMOTE_PHP_VERSION} ${HOST_REMOTE_PATH}/vendor/bin/drush sql:dump | gzip -5 -c > ${HOST_REMOTE_PATH}/backups/db/$(DB_REMOTE_NAME)_$(LOCALDATE).sql.gz"
	@echo ----------------------------------------------------------------
	@echo '✨ Database successfully dumped!'
	@echo ----------------------------------------------------------------
	@echo ----------------------------------------------------------------
	@echo 'Retrieving database dump successfull!'
	@echo ----------------------------------------------------------------
	mkdir -p backups/db
	make .exec-rsync-local-cmd SRC=${HOST_REMOTE_PATH}/backups/db/$(DB_REMOTE_NAME)_$(LOCALDATE).sql.gz DEST=./backups/db
	@echo ----------------------------------------------------------------
	@echo '✨ Remote dump successfully fetched!'
	@echo ----------------------------------------------------------------

.PHONY: ddev-import-db
ddev-import-db: ## Dump remote drupal database locally and import them in ddev database.
	@echo ----------------------------------------------------------------
	@echo '🔧 make ddev-import-db'
	@echo ----------------------------------------------------------------
	make .exec-ssh-cmd CMD="mkdir -p ${HOST_REMOTE_PATH}/backups/db"
	make .exec-ssh-cmd CMD="${HOST_REMOTE_PHP_VERSION} ${HOST_REMOTE_PATH}/vendor/bin/drush sql:dump | gzip -5 -c > ${HOST_REMOTE_PATH}/backups/db/$(DB_REMOTE_NAME)_$(LOCALDATE).sql.gz"
	@echo ----------------------------------------------------------------
	@echo '✨ Database successfully dumped!'
	@echo ----------------------------------------------------------------
	@echo ----------------------------------------------------------------
	@echo 'Retrieving database dump successfull!'
	@echo ----------------------------------------------------------------
	mkdir -p backups/db
	make .exec-rsync-local-cmd SRC=${HOST_REMOTE_PATH}/backups/db/$(DB_REMOTE_NAME)_$(LOCALDATE).sql.gz DEST=./backups/db
	@echo ----------------------------------------------------------------
	@echo '✨ Remote dump successfully fetched!'
	@echo ----------------------------------------------------------------
	ddev import-db --src=backups/db/$(DB_REMOTE_NAME)_$(LOCALDATE).sql.gz
	@echo ----------------------------------------------------------------
	@echo '✨ Database successfully imported!'
	@echo ----------------------------------------------------------------

.PHONY: help
help: ## Show a list of available commands
	@echo
	@printf '\033[34mAvailable commands:\033[0m\n'
	@grep -hE '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) |\
		sort |\
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo


.PHONY: .exec-ssh-cmd
.exec-ssh-cmd: ## Execute command over SSH
ifdef SSH_CONFIG
	ssh $(SSH_CONFIG) "$(CMD)"
else
	ssh -p $(SSH_PORT) -T $(SSH_USER)@$(SSH_HOST) "$(CMD)"
endif


.PHONY: .exec-rsync-local-cmd
.exec-rsync-local-cmd: ## Execute rsync command
ifdef SSH_CONFIG
	rsync -avzh --progress $(SSH_CONFIG):$(SRC) $(DEST)
else
	rsync -avzh --progress --port=$(SSH_PORT) $(SSH_USER)@$(SSH_HOST):$(SRC) $(DEST)
endif


.PHONY: .exec-rsync-distant-cmd
.exec-rsync-distant-cmd: ## Execute rsync command
ifdef SSH_CONFIG
	rsync -avzh --progress $(SRC) $(SSH_CONFIG):$(DEST)
else
	rsync -avzh --progress --port=$(SSH_PORT) $(SRC) $(SSH_USER)@$(SSH_HOST):$(DEST)
endif


# Push images to production
.PHONY: push-local-images
push-local-images: ## Push images to production
	@echo ----------------------------------------------------------------
	@echo '🔧 make push-local-images'
	@echo ----------------------------------------------------------------
	make .exec-rsync-distant-cmd SRC=./web/sites/default/files/. DEST=${HOST_REMOTE_PATH}/web/sites/default/files
	@echo ----------------------------------------------------------------
	@echo '✨ All site images were synced!'
	@echo ----------------------------------------------------------------


# Pull images from production
.PHONY: pull-production-images
pull-production-images: ## Pull images from production
	@echo ----------------------------------------------------------------
	@echo '🔧 make pull-production-images'
	@echo ----------------------------------------------------------------
	make .exec-rsync-local-cmd SRC=${HOST_REMOTE_PATH}/web/sites/default/files/. DEST=./web/sites/default/files
	@echo ----------------------------------------------------------------
	@echo '✨ All local images were synced!'
	@echo ----------------------------------------------------------------
