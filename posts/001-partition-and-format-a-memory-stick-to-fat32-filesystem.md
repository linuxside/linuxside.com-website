---
title: Partition and format a memory stick to FAT32 filesystem
date: 2021-09-02
tags: cli, fat32
featured: true
---

Partition and format via CLI to MS-DOS FAT filesystem any external USB drive (memory stick, HDD, SSD) and use it in daily life tasks.

---

Partitioning and formatting an external USB drive to FAT32 filesystem will make it supported in many devices or OS (Linux, Mac, Windows, etc).  
The OS I chose is **Debian**, but it will work in any Debian derived OS, including *Ubuntu*, *Raspbian OS*, *Linux Mint* and so on.  
Any of the commands below will run in **command line** (CLI) and they work with desktop and server enviroments.

1. install the dependencies

The `dosfstools` package contains the `mkfs.fat` and `fsck.fat` utilities, where the first one makes, while the second one checks the MS-DOS FAT filesystem.  
If this package is not installed, you can install it by running:

```bash
apt update
apt install dosfstools
```

2. Find out which drive is the USB drive

```bash
fdisk -l
```

In my case, the device is `/dev/sdb`. **Make sure it's the right device or you can loose the data on it!**

3. Delete and partition the drive

```bash
fdisk /dev/sdb
```

a. type **d** (delete) as many partitions there are  
b. type **n** (new) and choose primary, then hit enter twice  
c. type **w** (write)  
d. type **q** (quit)  

4. format to FAT32

```bash
mkfs.vfat /dev/sdb1
sync
eject /dev/sdb1
```

**Resources:**
- [dosfstools Debian homepage](https://tracker.debian.org/pkg/dosfstools)
- [dosfstools Github homepage](https://github.com/dosfstools/dosfstools)
