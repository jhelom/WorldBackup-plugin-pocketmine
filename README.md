# World Backup


![console](https://github.com/jhelom/WorldBackup-plugin-pocketmine/blob/develop/assets/console.png)

# English

This PocketMine-MP Plugin supports Backup and Restore World data.

Back up all the world automatically every day.

The history of the backup is recorded.

## Commands

Commands can only be run from the server console.

## Show List of Backups.

```
wbackup list
```

## Backup

```
wbackup backup [world]
```

## Restore

```
wbackup restore [world] [yyyy-mm-dd]
```

Restart the server using the STOP command to perform the restore.

This is because restoring will be incomplete if you change the world's file while the server is running.

If you want to cancel the restore, please use the "**wbackup clear**" command before restarting the server.
## Show Settings

```
wbackup set
```

## Set History Limit

```
wbackup set limit [int]
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
wbackup list
```

## バックアップする

```
wbackup backup [world]
```

## 復元する

```
wbackup restore [world] [yyyy-mm-dd]
```

**復元コマンド実行後にサーバーを再起動してください。**

*stopコマンドを使います*

次回サーバー起動時にワールドの復元処理が実行されます。

これは、サーバーが動作中時にワールドのファイルを書き換えると復元が不完全になるためです。

復元をキャンセルするには、サーバーを再起動する前に、次のコマンドを使います。

```
wbackup clear
```

## バックアップ設定を見る

```
wbackup set
```

## バックアップ履歴の上限数を設定する

```
wbackup set limit [int]
```

規定値は 10 です。

最低 3、最大 30 までの範囲で指定可能です。

バックアップされた履歴の世代数が上限を超えた場合、一番古い世代から削除されます。


# 仕様


バックアップされたワールドのファイルはプラグインフォルダの backups 配下に保存されます。


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