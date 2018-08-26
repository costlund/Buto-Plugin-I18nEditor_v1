# Buto-Plugin-I18nEditor_v1
Plugin page to edit i18n files.

## Settings
Data in theme settings.yml.

To call this plugin via url "/my_i18n_editor/start". Value "my_i18n_editor" can be anything.

```
plugin_modules:
  my_i18n_editor:
    plugin: i18n/editor_v1
```

Set this data if i18n folder is not in default location. Can be any location on server.

```
plugin:
  i18n:
    editor_v1:
      settings:
        path: '/data/theme/[theme]/i18n'
```

