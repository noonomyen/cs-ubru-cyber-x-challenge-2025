from base64 import b64encode

flag = b"CSUBRU{7h47_w45_fun_bu7_1_wund3r_h0w_l0ng_17_w0u1d_74k3_b3f0r3_f1nd1ng_17}"

for i in range(40):
    flag = b64encode(flag)

open("ask-real.txt", "w").write(flag.decode())
