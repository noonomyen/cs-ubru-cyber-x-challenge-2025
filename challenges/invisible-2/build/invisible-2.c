#include <stdio.h>
#include <sys/ptrace.h>

void rec(int i) {
    if (i == 0) return;
    rec(i - 1);
}

void loop1() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    while (i--) {}
}

void loop2() {
    volatile const char flag1[] = "4353554252557b35305f7930755f35373131315";
    (void)flag1;
    for (int i = 100000; i > 0; i--) {}
}

void loop3() {
    volatile const char _[] = "\n";
    (void)_;
    for (int i = 100000; i--;) ;
}

void loop4() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    do {
        i--;
    } while (i > 0);
}

void loop5() {
    volatile const char _[] = "\n";
    (void)_;
    rec(100000);
}

void loop6() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    start:
        if (i-- > 0) goto start;
}

void loop7() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    while (--i > 0) {}
}

void loop8() {
    volatile const char flag2[] = "f6d346e343633645f37305f66316e645f6d337d";
    (void)flag2;
    int i = 100000;
    for (; i-- > 0; ) ;
}

void loop9() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    for (;;) {
        if (i-- == 0) break;
    }
}

void loop10() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    while (1) {
        if (i == 0) break;
        i--;
    }
}

void loop11() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    while (i--) {}
}

void loop12() {
    volatile const char _[] = "\n";
    (void)_;
    for (int i = 100000; i > 0; i--) {}
}

void loop13() {
    volatile const char _[] = "\n";
    (void)_;
    for (int i = 100000; i--;) ;
}

void loop14() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    do {
        i--;
    } while (i > 0);
}

void loop15() {
    volatile const char _[] = "\n";
    (void)_;
    rec(100000);
}

void loop16() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    start:
        if (i-- > 0) goto start;
}

void loop17() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    while (--i > 0) {}
}

void loop18() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    for (; i-- > 0; ) ;
}

void loop19() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    for (;;) {
        if (i-- == 0) break;
    }
}

void loop20() {
    volatile const char _[] = "\n";
    (void)_;
    int i = 100000;
    while (1) {
        if (i == 0) break;
        i--;
    }
}

int main() {
    if (ptrace(PTRACE_TRACEME, 0, 0, 0) == -1) {
        return 1;
    }

    void (*loops[])(void) = {
        loop1, loop2, loop3, loop4, loop5,
        loop6, loop7, loop8, loop9, loop10,
        loop11, loop12, loop13, loop14, loop15,
        loop16, loop17, loop18, loop19, loop20
    };

    for (int i = 0; i < 20; i++) {
        loops[i]();
    }

    return 0;
}
