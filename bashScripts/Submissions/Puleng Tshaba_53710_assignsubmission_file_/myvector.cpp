#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;

MyVector::MyVector()
{
    n_allocated=0;
    data=nullptr;
    n_items=0;
}

MyVector::~MyVector()
{
    delete[] data;
}

size_t MyVector::size() const
{
    return n_items;
}

size_t MyVector::allocated_length() const
{
    return n_allocated;
}

void MyVector::push_back(const Thing &t)
{
    if(n_items+1>n_allocated){
	if(n_allocated==0){
            n_allocated=1;
            reallocate(n_allocated);
	}
	else{
            n_allocated=n_allocated*2;
            reallocate(n_allocated);
	}
    }
    ++n_items;
    data[n_items-1]=t;
}

void MyVector::pop_back()
{
    --n_items;
    if(n_items<0.25*n_allocated){
        n_allocated=n_allocated*0.5;
        reallocate(n_allocated);
    }
}

Thing &MyVector::front()
{
    return data[0];
}

Thing &MyVector::back()
{
    return data[n_items-1];
}

Thing *MyVector::begin()
{
    return &data[0];
}

Thing *MyVector::end()
{
    return &data[n_items];
}

Thing &MyVector::operator[](size_t i)
{
     return data[i];
}

Thing &MyVector::at(size_t i)
{
    if(i<=n_items){
        return data[i];
    }
    else{
        throw n_items;
    }
}

void MyVector::reallocate(size_t new_size)
{
    Thing *tmp=new Thing[new_size];
    int t=n_items;
    for(int i=0;i<t;++i){
        tmp[i]=data[i];
    }
    delete[] data;
    data=tmp;
}

