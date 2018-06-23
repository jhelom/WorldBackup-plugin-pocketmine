# World Backup

[![PoggitCI Badge](https://poggit.pmmp.io/ci.badge/jhelom/WorldBackup-plugin-pocketmine/WorldBackup)](https://poggit.pmmp.io/ci/jhelom/WorldBackup-plugin-pocketmine/WorldBackup)

---

Select Language: [English](#eng), [日本語](#jpn)

---

![console](https://github.com/jhelom/WorldBackup-plugin-pocketmine/blob/develop/assets/console.png)

---

<a id="eng" name="eng"></a>
# English

This is PocketMine-MP Plugin. 

Management backup and restore of world data.

Automatic backup with customizable schedule. Every day, weekly, monthly etc.

Backup History Rotated and archived.

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


## Set Backup Cycle Days

```
wbackup set days [int]
```

exsamples)

Backup at 1 day interval 30 history (about 1 month)

```
wbackup set days 1
wbackup set limit 30
```


Backup at weekly interval 15 histories (about 3 months)

```
wbackup set days 7
wbackup set limit 15
```

---


<a id=jpn" name="jpn"></a>
# 日本語

ワールドのバックアップと復元ができる PocketMine-MP のプラグインです。

全ワールドを自動でバックアップします。

バックアップする間隔と世代数は任意に設定できます。



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


## バックアップ周期の日数を設定する

```
wbackup set days [int]
```

設定例）

1日毎で30世代の場合（約1か月分）
```
wbackup set days 1
wbackup set limit 30
```

1週間毎で15世代の場合（約3か月分）
```
wbackup set days 7
wbackup set limit 15
```

# 構成


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