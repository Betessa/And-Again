#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;

MyVector::MyVector()
{
    data=nullptr;
    n_items=0;
    n_allocated=0;
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
    if(n_allocated==0){
        reallocate(1);
    }
    else if(n_allocated==n_items){
                    reallocate(n_allocated*2);

                }
                data[n_items]=t;
                n_items++;
}

void MyVector::pop_back()
{
    --n_items;
    if(n_items<n_allocated/4){
        reallocate(n_allocated/2);
    }
}

Thing &MyVector::front()
{
    return *data;
}

Thing &MyVector::back()
{
    return data[n_items-1];
}

Thing *MyVector::begin()
{
    return data;
}

Thing *MyVector::end()
{
    return (data+n_items);
}

Thing &MyVector::operator[](size_t i)
{
    return *(data+i);
}

Thing &MyVector::at(size_t i)
{
    if(i<n_items)
    {
        return *(data+i);

    }
    else{
        throw std::out_of_range("Requested index out of bounds.");
    }
}

void MyVector::reallocate(size_t new_size)
{
    Thing *temp =new Thing [new_size];
    for(size_t i=0;i<n_items;i=i+1){

        temp[i]=data[i];
    }
    delete[] data;
    data=temp;
    n_allocated=new_size;
}
