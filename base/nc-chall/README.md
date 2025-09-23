# NetCat Challenge Base

```sh
docker build -t icb-nc-chall .
```

```sh
source /nc-chall.sh
```

- `run` open TCP:1337 with socat
- `store_flag` write flag to file
- `rename_env` rename env var
- `unset_flag` remove GZCTF_FLAG
