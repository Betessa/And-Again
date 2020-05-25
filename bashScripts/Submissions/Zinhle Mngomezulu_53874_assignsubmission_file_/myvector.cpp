#include "myvector.h"


bool Thing::verbose = false;
size_t Thing::last_alloc = 0;

int i;


MyVector::MyVector()
{
   std::vector<int*>vect;
   vect.size()==0;
   vect.empty();
   int* vect=nullptr;

}

MyVector::~MyVector()
{
    delete[] vect;
}

size_t MyVector::size() const
{
    for(int i=0; i<=(vect.size); i++){

        std::cout<<vect.size()<<"\n";
    }

}

size_t MyVector::allocated_length() const
{
    buf_(std::vect<vect>(new vect[size]));
    max_size(size);

}

void MyVector::push_back(const Thing &t)
{
    vect.push_back();
    int* tmp = nullptr;

    tmp = new int<2 * vect_size>;

    for (int i = 0; i < vect_size; ++i) {
            tmp(i) = vect(i);
    }


    delete[] vect;

    vect = nullptr;

    vect = tmp;

    tmp = 0;

    vect_size *= 2;

}


