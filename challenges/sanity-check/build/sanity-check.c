#include <stdio.h>

int main() {
    setbuf(stdin, 0);
    setbuf(stdout, 0);
    setbuf(stderr, 0);

    FILE *f = fopen("flag", "r");
    char flag[256];
    if (f) {
        fgets(flag, sizeof(flag), f);
        fclose(f);

        printf("1+1=");
        int x;
        scanf("%d", &x);
        if (x == 2) {
            printf("%s\n", flag);
        }
    } else {
        printf("Internal error, Please contact admin\n");
    }

    fflush(stdout);

    return 0;
}
