#include <iostream>

#include "myvector.h"

using namespace std;

int main()
{
    MyVector v;
    Thing a(0);
    v.push_back(a);
    v.push_back(a);
    v.push_back(a);
    v.push_back(a);
    v.push_back(a);
    return 0;
}

