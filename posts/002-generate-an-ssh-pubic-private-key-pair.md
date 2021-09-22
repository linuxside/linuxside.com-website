---
title: Generate an SSH public/private key pair
date: 2021-09-22
tags: cli, ssh, ssh-keygen
featured: true
---

Generate private/public keys to securely authenticate the users over an SSH connection.

---

One of the best ways to tighten the security on an SSH server is to use private/public keys to securely authenticate the users and to disable the username/password logins.

The OS I chose to generate the SSH key pair is Ubuntu, but I think this will work under any Linux distribution (Debian derived distributions for sure).

To generate the key pair, run:

```bash
ssh-keygen -t rsa -C "Connect to X server from Y computer"
```

The `-C` option is to set a comment that will make the public key easier to spot between other public keys. Without setting this, the `ssh-keygen` command will still add a comment that will be of `<user>@<hostname>` format (the user you are running under and the computer hostname your are generating the key pair under).

The command above will ask you for the path to the new key pair. If you don't add your custom path, the default will generate 2 files (`id_rsa` and `id_rsa.pub` respectively) under `/home/user/.ssh` folder (where `user` is the user home folder).

**Note:** the `.pub` file will be the public key that you'll have to copy over to the remote SSH server. This can be done with copy/paste or securely via `ssh-copy-id` command, like this:

```bash
ssh-copy-id -i /home/user/.ssh/id_rsa.pub <user>@<your-remote-host>
```

**Resources:**
- [IBM - generating an SSH key pair](https://www.ibm.com/docs/en/flashsystem-5x00/8.2.x?topic=pscalh-generating-ssh-key-pair-using-openssh-3)
- [Generate SSH Key With ssh-keygen](https://www.geeksforgeeks.org/how-to-generate-ssh-key-with-ssh-keygen-in-linux/)
- [ssh-keygen manual](https://www.man7.org/linux/man-pages/man1/ssh-keygen.1.html)
