#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;

MyVector::MyVector()
{
    data = nullptr;
    n_items = 0;
    n_allocated = 0;
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
    if(n_allocated == 0){
        reallocate(1);
    }

    if (n_items == n_allocated){
        reallocate(2*n_allocated);
    }

    data[n_items] = t;
    n_items = n_items+1;
}


void MyVector::pop_back()
{
    if(n_items == 0){
         throw std::out_of_range("Requested index out of bounds.");
    }

    n_items = n_items - 1;

    if(n_items < 0.25*n_allocated){
        reallocate(n_allocated/2);
    }
}

Thing &MyVector::front()
{
    return *data;
}

Thing &MyVector::back()
{
    return *(data + n_items - 1);
}

Thing *MyVector::begin()
{
    return data;
}


Thing *MyVector::end()
{
    return data + n_items;
}

Thing &MyVector::operator[](size_t i)
{
    return data[i];
}

Thing &MyVector::at(size_t i)
{
    if(i < n_items){
        return data[i];
    }
    throw std::out_of_range("Requested index out of bounds.");

}

void MyVector::reallocate(size_t new_size)
{
    Thing* d = new Thing[new_size];
    n_allocated = new_size;
    for(int i = 0; i < n_items; i++)
    {
        d[i] = data[i];
    }
    delete[] data;
    data = d;
}

