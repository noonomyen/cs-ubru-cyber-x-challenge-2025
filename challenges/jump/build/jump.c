#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>

void print_flag() {
    FILE *fp = fopen("flag.txt", "r");
    if (!fp) { perror("flag.txt"); exit(1); }
    char tmp[128];
    if (fgets(tmp, sizeof(tmp), fp)) {
        printf("FLAG: %s\n", tmp);
    }
    fclose(fp);
}

int main() {
    setbuf(stdin, 0);
    setbuf(stdout, 0);
    setbuf(stderr, 0);

    char buf[100];
    printf("Enter your name: ");
    read(0, buf, 0x100);
    printf("Hello %s", buf);

    return 0;
}
