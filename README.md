# World Backup

# English

This Plug-in supports Backup and Restoration of World data.

Back up all the world automatically every day.

The history of the backup is recorded.

## Commands

Commands can only be run from the server console.

## Show List of Backups.

```
/wbackup list
```

## Backup

```
/wbackup backup [world]
```

## Restore

```
/wbackup restore [world] [yyyy-mm-dd]
```

**Restart the server after executing the restore command.**

*Use the stop command*

The restoration process of the world will be executed the next time the server starts up.

This is because restoring will be incomplete if you change the world's file while the server is running.

## Show Settings

```
/wbackup set
```

## Set History Limit

```
/wbackup set max [int]
```

The default value is 10.

The history limit can be set in a range from at least 3 to a maximum of 30.

If the number of histories backed up exceeds the upper limit, it will be deleted from the oldest history.


---


# 日本語

ワールドのバックアップと復元ができる PocketMine-MP のプラグインです。

毎日１日１回、全ワールドを自動でバックアップします。

バックアップは世代管理できます。


## コマンド

コマンドはサーバーコンソールからのみ実行できます。

## バックアップの一覧を見る

```
/wbackup list
```

## バックアップする

```
/wbackup backup [world]
```

## 復元する

```
/wbackup restore [world] [yyyy-mm-dd]
```

**復元コマンド実行後にサーバーを再起動してください。**

*stopコマンドを使います*

次回サーバー起動時にワールドの復元処理が実行されます。

これは、サーバーが動作中時にワールドのファイルを書き換えると復元が不完全になるためです。


## バックアップ設定を見る

```
/wbackup set
```

## バックアップ履歴の上限数を設定する

```
/wbackup set max [int]
```

規定値は 10 です。

最低 3、最大 30 までの範囲で指定可能です。

バックアップされた履歴の世代数が上限を超えた場合、一番古い世代から削除されます。


# 仕様


バックアップされたワールドのファイルはプラグインフォルダの WorldBackup 下の backups に保存されます。


```
plugins
   |
   +--- WorldBackup
           |
           +--- backups
                   |
                   +--- world
                          |
                          +--- yyyy-mm-dd
                          +--- yyyy-mm-dd
                          +--- yyyy-mm-dd
```