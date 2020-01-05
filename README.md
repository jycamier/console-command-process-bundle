console-command-process-bundle
===

Simple Task to run Console command for Process Bundle

# Use case : install the app

At first, define a yaml file which contains a list a symfony console commands to run to install project.
Please to respect the formalism waiting by `Symfony\Bundle\FrameworkBundle\Console\Application` as : 

```yaml
## '%kernel.project_dir%/config/install.yaml'

- command: doctrine:database:drop
  --if-exists: true
  --force: true
  --no-ansi: true 
  --no-interaction: true
- command: doctrine:database:create
  --if-not-exists: true
  --no-ansi: true 
  --no-interaction: true
- command: doctrine:schema:create
  --no-ansi: true 
  --no-interaction: true
- command: assets:install
  target: public
  --symlink: true
  --relative: true
```

Then, define a configuration for an install process

```yaml
## '%kernel.project_dir%/config/packages/process.yml'

clever_age_process:
    configurations:
        install:
            entry_point: load_install_command_file
            tasks:
                load_install_command_file:
                    service: '@CleverAge\ProcessBundle\Task\File\YamlReaderTask'
                    options:
                        file_path: '%kernel.project_dir%/config/install.yaml'
                    outputs: [run_command]
                run_command:
                    service: '@Jycamier\ConsoleCommandProcessBundle\Task\ConsoleApplicationTask'
                    options:
                        debug_mode: true
```

You can not install your app

```bash
$ bin/console cleverage:process:execute install
```

## Todo
* add a way to inject standards args to configure the installation process
 
 

